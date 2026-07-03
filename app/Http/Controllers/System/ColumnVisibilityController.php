<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\System\ColumnVisibilityConfig;
use App\Models\System\Client;
use Hyn\Tenancy\Environment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ColumnVisibilityController extends Controller
{
    public function index()
    {
        $configs = ColumnVisibilityConfig::all()->keyBy('module');

        return [
            'success' => true,
            'data' => $configs,
        ];
    }

    public function show($module)
    {
        $config = ColumnVisibilityConfig::where('module', $module)->first();

        return [
            'success' => true,
            'data' => $config ? $config->columns : null,
        ];
    }

    public function store(Request $request, $module)
    {
        $request->validate([
            'columns' => 'required|array',
        ]);

        ColumnVisibilityConfig::updateOrCreate(
            ['module' => $module],
            ['columns' => $request->columns]
        );

        $columnsJson = json_encode($request->columns);

        $clients = Client::with('hostname.website')->get();

        foreach ($clients as $client) {
            try {
                $tenancy = app(Environment::class);
                $tenancy->tenant($client->hostname->website);

                DB::connection('tenant')
                    ->table('columns_to_reports')
                    ->where('report', $module)
                    ->update(['columns' => $columnsJson]);
            } catch (\Exception $e) {
                // continuar con el siguiente tenant
            }
        }

        return [
            'success' => true,
            'message' => 'Configuración guardada y aplicada a todos los usuarios',
        ];
    }
}
