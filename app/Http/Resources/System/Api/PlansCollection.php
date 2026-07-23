<?php

namespace App\Http\Resources\System\Api;

use App\Models\System\Module;
use App\Models\System\Plan;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PlansCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection->transform(function (Plan $row) {

            $modules = optional($row->module_permissions)->modules;
            $arr_modules = null;

            if ($modules) {
                $arr_modules = Module::whereIn('id', $modules)->get();
                // dump($arr_modules);
            }

            return [
                'id' => $row->id,
                'name' => $row->name,
                'pricing' => $row->pricing,
                'modules' => $arr_modules ? $arr_modules->transform(function (Module $row) {
                    return [
                        'id' => $row->id,
                        'name' => $row->description,
                    ];
                }) : null,
                'limit_users' => $row->limit_users,
                'test_days' => $row->test_days,
                'limit_documents' => $row->limit_documents,
                'url_register' => route('guest.register.index', [
                    'plan_id' => $row->id
                ])
            ];
        });
    }
}
