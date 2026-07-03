<?php

// Modules/ClaimsBook/Http/Resources/ClaimResource.php

namespace Modules\ClaimsBook\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClaimResource extends JsonResource
{
    public function toArray($request): array
    {
        return $this->getCollectionData();
    }
}
