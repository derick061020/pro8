<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\JsonResource;

class PromotionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'name' => $this->name,
            'status' => $this->status,
            'type'=> $this->type,
            'image' => $this->image,
            'image_url' => $this->image_url,
            'item_id' => $this->item_id,
            'category_id' => $this->category_id,
            'custom_link' => $this->custom_link,
            'spot_url' => $this->spot_url,
        ];
    }
}