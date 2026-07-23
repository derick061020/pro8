<?php
namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Catalogs\OperationType;

class OperationTypeController extends Controller
{
    public function records()
    {
        return OperationType::orderBy('id')->get()->transform(function ($row) {
            return [
                'id' => $row->id,
                'description' => $row->description,
                'exportation' => (bool) $row->exportation,
                'active' => (bool) $row->active,
            ];
        });
    }

    public function changeActive($id, $active)
    {
        $record = OperationType::findOrFail($id);
        $record->active = (bool) $active;
        $record->save();

        return [
            'success' => true,
            'message' => 'Tipo de operacion actualizado correctamente',
        ];
    }
}
