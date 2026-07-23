<?php
namespace App\Http\Controllers\System\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\System\Api\PlansCollection;
use App\Models\System\Plan;

class PlanController extends Controller
{
    public function records()
    {
        $records = Plan::whereNot('id', 1)->get();
        return new PlansCollection($records);
    }
}