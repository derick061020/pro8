<?php

// Modules/ClaimsBook/Http/Resources/ClaimCollection.php

namespace Modules\ClaimsBook\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ClaimCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return $this->collection->transform(function ($row) {
            return $row->getCollectionData();
        })->toArray();
    }
}
