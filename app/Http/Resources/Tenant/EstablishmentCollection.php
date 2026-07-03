<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\ResourceCollection;

class EstablishmentCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function toArray($request)
    {
        return $this->collection->transform(function($row, $key) {
            return [
                'id'          => $row->id,
                'code'        => $row->code,
                'description' => $row->description,
                'address'     => $row->address_full,
                'telephone'   => ($row->telephone && $row->telephone !== '-') ? $row->telephone : null,
                'email'       => ($row->email && $row->email !== '-') ? $row->email : null,
            ];
        });
    }
}