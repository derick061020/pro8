<?php

namespace Modules\Hotel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HotelRoomRequest extends FormRequest
{
	public function authorize()
	{
		return true;
	}

	public function rules()
	{
		$rules = [
            'name' => [
                'required', 
                'max:50', 
                Rule::unique('tenant.hotel_rooms', 'name')
                    ->where(function ($query){
                        $query->where('establishment_id', $this->establishment_id);
                    })
                    ->ignore($this->id),
            ],
			'hotel_category_id' => ['required', 'numeric', 'exists:tenant.hotel_categories,id'],
			'hotel_floor_id'    => ['required', 'numeric', 'exists:tenant.hotel_floors,id'],
			'item_id' 			=> '',
			'description'       => 'nullable|max:250',
			'active'            => 'required|boolean',
            'establishment_id' 	=> 'required|exists:tenant.establishments,id',
            // Campos de la web pública de reservas (landing). Todos opcionales.
            'short_description' => 'nullable|string|max:255',
            'capacity'          => 'nullable|integer|min:1|max:50',
            'beds'              => 'nullable|string|max:100',
            'size'              => 'nullable|integer|min:1|max:9999',
            'featured'          => 'nullable|boolean',
            'web_visible'       => 'nullable|boolean',
            'amenities'         => 'nullable|array',
            'amenities.*'       => 'nullable|string|max:80',
            'images'            => 'nullable|array',
            'images.*.filename' => 'nullable|string',
            'images.*.temp_path'=> 'nullable|string',
        ];

        return $rules;
	}

	protected function prepareForValidation()
    {
        $user = auth()->user();

        if ($user->type !== 'admin') {
            $this->merge([
                'establishment_id' => $user->establishment_id,
            ]);
        }
    }
}
