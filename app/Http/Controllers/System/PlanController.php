<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; 
use App\Models\System\Plan;
use App\Models\System\PlanDocument;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\System\PlanCollection;
use App\Http\Resources\System\PlanResource;
use App\Http\Requests\System\PlanRequest;
use App\Models\System\Module;

class PlanController extends Controller
{
    public function index()
    {
        return view('system.plans.index');
    }

    
    public function records()
    {
        $records = Plan::all();

        return new PlanCollection($records);
    }

    public function record($id)
    {
        $record = new PlanResource(Plan::findOrFail($id));

        return $record;
    }

    public function popular()
    {
        return [
            'popular_plan' => Plan::where('is_popular', true)->select('id', 'name')->first(),
        ];
    }

    public function tables()
    {
        $plan_documents = PlanDocument::all(); 

        $modules = Module::with('levels')
            ->where('sort', '<', 14)
            ->where('value', '!=', 'production_app')
            ->orderBy('sort')
            ->get()
            ->each(function ($module) {
                return $this->prepareModules($module);
            });

        $apps = Module::with('levels')
            ->where('sort', '>', 13)
            ->where('value', '!=', 'production_app')
            ->orderBy('sort')
            ->get()
            ->each(function ($module) {
                return $this->prepareModules($module);
            });

        $group_basic = Module::with('levels')
            ->whereIn('id', [7,1,6,17,18,5,14])
            ->orderBy('sort')
            ->get()
            ->each(function ($module) {
                return $this->prepareModules($module);
            });
        $group_hotel = Module::with('levels')
            ->whereIn('id', [7,1,6,17,18,5,14,8,4])
            ->orderBy('sort')
            ->get()
            ->each(function ($module) {
                return $this->prepareModules($module);
            });
        $group_pharmacy = Module::with('levels')
            ->whereIn('id', [7,1,6,17,18,5,14,8,4])
            ->orderBy('sort')
            ->get()
            ->each(function ($module) {
                return $this->prepareModules($module);
            });
        $group_restaurant = Module::with('levels')
            ->whereIn('id', [7,1,6,17,18,5,14,8,4])
            ->orderBy('sort')
            ->get()
            ->each(function ($module) {
                return $this->prepareModules($module);
            });
        $group_hotel_apps = Module::with('levels')
            ->whereIn('id', [15])
            ->orderBy('sort')
            ->get()
            ->each(function ($module) {
                return $this->prepareModules($module);
            });
        $group_pharmacy_apps = Module::with('levels')
            ->whereIn('id', [19])
            ->orderBy('sort')
            ->get()
            ->each(function ($module) {
                return $this->prepareModules($module);
            });
        $group_restaurant_apps = Module::with('levels')
            ->whereIn('id', [23])
            ->orderBy('sort')
            ->get()
            ->each(function ($module) {
                return $this->prepareModules($module);
            });

        $popular_plan = Plan::where('is_popular', true)->select('id', 'name')->first();

        return compact(
            'plan_documents',
            'modules',
            'apps',
            'group_basic',
            'group_hotel',
            'group_pharmacy',
            'group_restaurant',
            'group_hotel_apps',
            'group_pharmacy_apps',
            'group_restaurant_apps',
            'popular_plan'
        );
    }

    private function prepareModules(Module $module): Module
    {
        $levels = [];
        foreach ($module->levels as $level) {
            array_push($levels, [
                'id' => "{$module->id}-{$level->id}",
                'description' => $level->description,
                'module_id' => $level->module_id,
                'is_parent' => false,
            ]);
        }
        unset($module->levels);
        $module->is_parent = true;
        $module->childrens = $levels;
        return $module;
    }


    public function store(PlanRequest $request)
    {
        $id = $request->input('id');
        $plan = Plan::firstOrNew(['id' => $id]);
        $plan->fill($request->all());

        if ($request->has('module_permissions')) {
            $plan->module_permissions = $request->input('module_permissions');
        }

        $plan->save();

        return [
            'success' => true,
            'message' => ($id)?'Plan editado con éxito':'Plan registrado con éxito'
        ];
    }

    public function destroy($id)
    {
        $plan = Plan::findOrFail($id);
        $plan->delete();

        return [
            'success' => true,
            'message' => 'Plan eliminado con éxito'
        ];
    }

}
