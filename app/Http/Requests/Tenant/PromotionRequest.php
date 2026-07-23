<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PromotionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->input('id');
     
        return [
            'name' => [
                'required'
            ],
            'description' => [
                'nullable'
            ],
            'item_id' => [
                'nullable',
                'integer',
                'exists:tenant.items,id'
            ],
            'category_id' => [
                'nullable',
                'integer',
                'exists:tenant.categories,id'
            ],
            'custom_link' => [
                'nullable',
                'string'
            ],
            'image' => [
                $id ? 'nullable' : 'required'
            ]
        ];
    }

    public function messages()
    {
        return [
            'item_id.integer' => 'El campo Producto debe ser un número.',
            'item_id.exists' => 'El producto seleccionado no existe.',
            'category_id.integer' => 'El campo Categoría debe ser un número.',
            'category_id.exists' => 'La categoría seleccionada no existe.',
        ];
    }
}