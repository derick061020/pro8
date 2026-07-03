<?php

// Modules/ClaimsBook/Http/Resources/ClaimChannelResource.php

namespace Modules\ClaimsBook\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClaimChannelResource extends JsonResource
{
    public function toArray($request): array
    {
        return $this->getCollectionData();
    }
}
