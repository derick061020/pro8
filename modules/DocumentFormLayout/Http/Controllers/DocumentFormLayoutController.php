<?php

namespace Modules\DocumentFormLayout\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\DocumentFormLayout\Models\DocumentFormLayout;
use Modules\DocumentFormLayout\Http\Requests\UpdateDocumentFormLayoutRequest;

class DocumentFormLayoutController extends Controller
{
    public function show($variant)
    {
        if (! in_array($variant, DocumentFormLayout::ALLOWED_VARIANTS, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Variante no válida.',
            ], 422);
        }

        $record = DocumentFormLayout::where('variant', $variant)->first();

        return [
            'success' => true,
            'data' => [
                'variant'       => $variant,
                'pinned_fields' => $record ? $record->pinned_fields : [],
            ],
        ];
    }

    public function update(UpdateDocumentFormLayoutRequest $request, $variant)
    {
        $payload = $this->normalize($request->input('pinned_fields', []));

        $record = DocumentFormLayout::updateOrCreate(
            ['variant' => $variant],
            [
                'pinned_fields'      => $payload,
                'updated_by_user_id' => optional(auth()->user())->id,
            ]
        );

        return [
            'success' => true,
            'message' => 'Configuración guardada con éxito',
            'data' => [
                'variant'       => $record->variant,
                'pinned_fields' => $record->pinned_fields,
            ],
        ];
    }

    protected function normalize(array $fields): array
    {
        usort($fields, fn ($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));

        return array_values(array_map(function ($row, $index) {
            return [
                'field_key' => $row['field_key'],
                'width'     => (int) $row['width'],
                'order'     => $index,
            ];
        }, $fields, array_keys($fields)));
    }
}
