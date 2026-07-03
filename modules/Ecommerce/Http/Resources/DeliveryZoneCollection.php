<?php

namespace Modules\Ecommerce\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Colección paginada de zonas de delivery.
 */
class DeliveryZoneCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => $this->collection->transform(fn ($zone) => $zone->getCollectionData()),
        ];
    }
}
