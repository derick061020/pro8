<?php

namespace Modules\Report\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\BusinessTurn\Models\DocumentHotel;
use Modules\Hotel\Models\HotelRent;

class RentHotelCollection extends ResourceCollection
{


    public function toArray($request) {


        return $this->collection->transform(function(HotelRent $row, $key){
            $data = $row->toArray();
            $customer = $row->customer;
            $room = $row->room;
            $items = $row->products;
            $rent_items = $row->items;
            $rental_total = $rent_items
                ->where('type', 'HAB')
                ->sum(function ($item) {
                    $json_total = isset($item->item->total) ? (float) $item->item->total : 0;
                    $column_total = isset($item->total) ? (float) $item->total : 0;

                    return $json_total > 0 ? $json_total : $column_total;
                });
            $data['status'] = ucfirst(strtolower($data['status']));
            $data['customer'] = $customer;
            $data['room'] = $room;
            $data['items'] = $items;
            $data['rent_items'] = $rent_items;
            $data['rental_total'] = $rental_total;
            return  $data;

        });
    }
}
