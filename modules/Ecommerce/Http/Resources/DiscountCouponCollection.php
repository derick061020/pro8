<?php

namespace Modules\Ecommerce\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DiscountCouponCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return $this->collection->transform(function ($row) {
            return $row->getCollectionData();
        })->toArray();
    }
}
