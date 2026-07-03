<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\System\ColumnVisibilityConfig;
use App\Models\Tenant\ColumnsToReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ColumnVisibilityController extends Controller
{
    public function show($module)
    {
        $userId = Auth::id() ?? 0;

        $record = ColumnsToReport::where('user_id', $userId)
            ->where('report', $module)
            ->first();

        if ($record) {
            return ['success' => true, 'data' => $record->columns];
        }

        $systemConfig = ColumnVisibilityConfig::where('module', $module)->first();

        return [
            'success' => true,
            'data' => $systemConfig ? $systemConfig->columns : null,
        ];
    }

    public function store(Request $request, $module)
    {
        $request->validate(['columns' => 'required|array']);

        $userId = Auth::id() ?? 0;

        ColumnsToReport::updateOrCreate(
            ['user_id' => $userId, 'report' => $module],
            ['columns' => $request->columns]
        );

        return ['success' => true];
    }
}
