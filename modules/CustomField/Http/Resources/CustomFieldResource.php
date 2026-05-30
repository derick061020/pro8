<?php

namespace Modules\CustomField\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomFieldResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'type' => $this->type,
            'required' => $this->required,
            'options' => $this->options,
            'order' => $this->order,
            'enabled_for_documents' => $this->enabled_for_documents,
            'enabled_for_sale_notes' => $this->enabled_for_sale_notes,
            'enabled_for_dispatches' => $this->enabled_for_dispatches,
            'enabled_for_order_notes' => $this->enabled_for_order_notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
