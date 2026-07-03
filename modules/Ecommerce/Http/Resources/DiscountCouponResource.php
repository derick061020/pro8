<?php

namespace Modules\Ecommerce\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DiscountCouponResource extends JsonResource
{
    public function toArray($request): array
    {
        return $this->getCollectionData();
    }
}
