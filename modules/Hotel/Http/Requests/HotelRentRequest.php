<?php

namespace Modules\Hotel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HotelRentRequest extends FormRequest
{
	public function authorize()
	{
		return true;
	}

	public function rules()
	{
		return [
			'customer_id'              => 'required|numeric',
			'customer'                 => 'required',
			'customer.name'            => 'required',
			'customer.address'         => 'nullable|string|max:255',
			'notes'                    => 'max:250',
			'adults'                   => 'required|numeric|min:1',
			'children'                  => 'required|numeric|min:0',
			'towels'                   => 'required|numeric|min:1',
			'license_plate'            => 'nullable|string|max:20',
			'travel_reason'            => 'nullable|in:visita,trabajo,estudio,religion,salud,compras,otros',
			'duration'                 => 'required|numeric|min:1',
			'quantity_persons'         => 'required|numeric|min:1',
			'payment_status'           => 'required|in:PAID,DEBT,ADVANCE',
			'input_date'               => 'required|date_format:Y-m-d',
			'input_time'               => 'required|date_format:H:i',
			'output_date'              => 'required|date_format:Y-m-d',
			'output_time'              => 'required|date_format:H:i',
			'product'                  => 'required',
			'hotel_rate_id'            => 'required|numeric',
			'affectation_igv_type_id'  => 'required',
            'rent_payment.payment_method_type_id' => 'required_if:payment_status,PAID,ADVANCE',
            'rent_payment.payment_destination_id' => 'required_if:payment_status,PAID,ADVANCE',
            'rent_payment.payment' 	   => 'required_if:payment_status,PAID,ADVANCE',
			'data_persons'         	   => 'required',
			'rental_date_time'         => 'nullable|date',
			'rental_price'            => 'nullable|numeric|min:0',
			'rental_period_type'      => 'nullable|in:hour,day,month',
		];
	}

	    
    /**
     *
     * @return array
     */
    public function messages()
    {
        return [
            'rent_payment.payment_method_type_id.required_if' => 'El campo m. pago es obligatorio cuando estado de pago es PAGADO o ADELANTO.',
            'rent_payment.payment_destination_id.required_if' => 'El campo destino es obligatorio cuando estado de pago es PAGADO o ADELANTO.',
            'rental_date_time.date' => 'La fecha y hora de renta debe ser una fecha válida.',
            'rental_price.numeric' => 'El precio de renta debe ser un número.',
            'rental_price.min' => 'El precio de renta debe ser mayor o igual a 0.',
            'rental_period_type.in' => 'El tipo de período debe ser: hora, día o mes.',
        ];
    }
	
}
