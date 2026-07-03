<?php

namespace Modules\Ecommerce\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Recurso API para una zona de delivery individual.
 */
class DeliveryZoneResource extends JsonResource
{
    public function toArray($request): array
    {
        return $this->resource->getCollectionData();
    }
}
