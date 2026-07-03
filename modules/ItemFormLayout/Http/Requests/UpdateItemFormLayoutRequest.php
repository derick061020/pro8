<?php

namespace Modules\ItemFormLayout\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\ItemFormLayout\Models\ItemFormLayout;

class UpdateItemFormLayoutRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'pinned_fields'              => 'present|array',
            'pinned_fields.*.field_key'  => 'required|string|max:64',
            'pinned_fields.*.width'      => 'required|integer|min:1|max:12',
            'pinned_fields.*.order'      => 'required|integer|min:0',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $variant = $this->route('variant');
            if (! in_array($variant, ItemFormLayout::ALLOWED_VARIANTS, true)) {
                $validator->errors()->add('variant', 'Variante no válida.');
            }

            $fields = $this->input('pinned_fields', []);
            $keys = array_column($fields, 'field_key');
            if (count($keys) !== count(array_unique($keys))) {
                $validator->errors()->add('pinned_fields', 'Hay field_key duplicados.');
            }
        });
    }
}
