<?php

namespace Modules\FullSuscription\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SuscriptionOrderCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->transform(function ($row) {
            return $row->getCollectionData();
        });
    }
}
