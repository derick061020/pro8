<?php

// Modules/ClaimsBook/Http/Resources/StatusClaimResource.php

namespace Modules\ClaimsBook\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StatusClaimResource extends JsonResource
{
    public function toArray($request): array
    {
        return $this->getCollectionData();
    }
}
