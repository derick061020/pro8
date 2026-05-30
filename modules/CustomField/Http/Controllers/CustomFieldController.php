<?php

namespace Modules\CustomField\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CustomField\Models\CustomField;
use Modules\CustomField\Http\Requests\CustomFieldRequest;
use Modules\CustomField\Http\Resources\CustomFieldResource;

class CustomFieldController extends Controller
{
    /**
     * Mostrar la vista principal de campos personalizados
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('customfield::index');
    }

    /**
     * Obtener las columnas de la tabla
     *
     * @return array
     */
    public function columns()
    {
        return [
            'name' => 'Nombre',
            'type' => 'Tipo',
            'required' => 'Requerido',
        ];
    }

    /**
     * Obtener listado paginado de campos personalizados
     *
     * @param Request $request
     * @return \Modules\CustomField\Http\Resources\CustomFieldCollection
     */
    public function records(Request $request)
    {
        $column = $request->column ?? 'name';
        $value = $request->value ?? '';

        $records = CustomField::where($column, 'like', "%{$value}%")
            ->orderBy('order', 'asc')
            ->paginate(config('tenant.items_per_page'));

        return response()->json([
            'data' => CustomFieldResource::collection($records),
            'current_page' => $records->currentPage(),
            'per_page' => $records->perPage(),
            'total' => $records->total(),
            'last_page' => $records->lastPage(),
        ]);
    }

    /**
     * Obtener un campo personalizado específico
     *
     * @param int $id
     * @return CustomFieldResource
     */
    public function record($id)
    {
        $record = CustomField::findOrFail($id);
        return new CustomFieldResource($record);
    }

    /**
     * Guardar o actualizar un campo personalizado
     *
     * @param CustomFieldRequest $request
     * @return array
     */
    public function store(CustomFieldRequest $request)
    {
        $id = $request->input('id');
        $record = CustomField::firstOrNew(['id' => $id]);

        // Generar slug a partir del nombre
        $slug = \Illuminate\Support\Str::slug($request->input('name'), '-');
        $record->fill($request->validated());
        $record->slug = $slug;

        // Si no es actualización, encontrar el máximo order y sumar 1
        if (!$id) {
            $record->order = CustomField::max('order') + 1;
        }

        $record->save();

        return [
            'success' => true,
            'message' => ($id) ? 'Campo personalizado editado con éxito' : 'Campo personalizado registrado con éxito',
            'data' => new CustomFieldResource($record),
        ];
    }

    /**
     * Eliminar un campo personalizado
     *
     * @param int $id
     * @return array
     */
    public function destroy($id)
    {
        $record = CustomField::findOrFail($id);
        $record->delete();

        return [
            'success' => true,
            'message' => 'Campo personalizado eliminado con éxito',
        ];
    }

    /**
     * Actualizar el estado de un documento para un campo
     *
     * @param Request $request
     * @return array
     */
    public function updateDocumentStatus(Request $request)
    {
        $id = $request->input('id');
        $document = $request->input('document');
        $enabled = $request->input('enabled');

        $record = CustomField::findOrFail($id);

        // Mapeo de documentos a columnas
        $documentMap = [
            'documents' => 'enabled_for_documents',
            'sale_notes' => 'enabled_for_sale_notes',
            'dispatches' => 'enabled_for_dispatches',
            'order_notes' => 'enabled_for_order_notes',
        ];

        if (!isset($documentMap[$document])) {
            return [
                'success' => false,
                'message' => 'Documento no válido',
            ];
        }

        $column = $documentMap[$document];
        $record->update([$column => $enabled]);

        return [
            'success' => true,
            'message' => 'Estado actualizado con éxito',
            'data' => new CustomFieldResource($record),
        ];
    }
}
