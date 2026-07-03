<?php

// Modules/ClaimsBook/Http/Resources/StatusClaimCollection.php

namespace Modules\ClaimsBook\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class StatusClaimCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return $this->collection->transform(function ($row) {
            return $row->getCollectionData();
        })->toArray();
    }
}
