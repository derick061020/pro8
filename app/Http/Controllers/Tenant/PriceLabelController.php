<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\PriceLabel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class PriceLabelController extends Controller
{
    /**
     * Listar todos los price labels ordenados por position
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $labels = PriceLabel::ordered()->get();

        return response()->json([
            'success' => true,
            'data' => $labels->map->getCollectionData(),
        ]);
    }

    /**
     * Listar solo price labels activos (para usar en formularios de venta)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function active()
    {
        $labels = PriceLabel::active()->ordered()->get();

        return response()->json([
            'success' => true,
            'data' => $labels->map->getCollectionData(),
        ]);
    }

    /**
     * Crear nuevo price label
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            DB::connection('tenant')->beginTransaction();

            // Obtener el siguiente position
            $nextPosition = PriceLabel::max('position') + 1;

            $priceLabel = PriceLabel::create([
                'position' => $nextPosition,
                'label' => $request->label,
                'is_active' => $request->is_active ?? true,
            ]);

            DB::connection('tenant')->commit();

            return response()->json([
                'success' => true,
                'message' => 'Etiqueta de precio creada correctamente',
                'data' => $priceLabel->getCollectionData(),
            ]);

        } catch (Exception $e) {
            DB::connection('tenant')->rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al crear la etiqueta de precio: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Actualizar price label existente
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'label' => 'required|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            $priceLabel = PriceLabel::findOrFail($id);

            $newIsActive = $request->is_active ?? $priceLabel->is_active;
            $payload = [
                'label' => $request->label,
                'is_active' => $newIsActive,
            ];

            if (!$newIsActive && $priceLabel->is_default) {
                $payload['is_default'] = false;
            }

            $priceLabel->update($payload);

            return response()->json([
                'success' => true,
                'message' => 'Etiqueta de precio actualizada correctamente',
                'data' => $priceLabel->fresh()->getCollectionData(),
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la etiqueta de precio: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Eliminar price label
     * No permite eliminar los 3 labels originales (ID 1, 2, 3)
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $priceLabel = PriceLabel::findOrFail($id);

            // Verificar si está en uso
            $inUseCount = $priceLabel->itemUnitTypePrices()->count();

            if ($inUseCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "No se puede eliminar. Esta etiqueta está siendo usada en {$inUseCount} precio(s) de productos",
                ], 422);
            }

            DB::connection('tenant')->beginTransaction();

            $priceLabel->delete();

            // Re-organizar positions para mantener secuencia continua
            $this->reorganizePositions();

            DB::connection('tenant')->commit();

            return response()->json([
                'success' => true,
                'message' => 'Etiqueta de precio eliminada correctamente',
            ]);

        } catch (Exception $e) {
            DB::connection('tenant')->rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la etiqueta de precio: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Limpiar el flag is_default de todos los price labels.
     * Esto hace que "Precio Principal" vuelva a ser el default por descarte.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearDefault()
    {
        try {
            PriceLabel::where('is_default', true)->update(['is_default' => false]);

            return response()->json([
                'success' => true,
                'message' => 'Precio Principal marcado como por defecto',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al limpiar la etiqueta por defecto: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Marcar un price label como default (toggle).
     * Si ya es default, lo desmarca. Si no, lo marca y limpia los demás.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function setDefault($id)
    {
        try {
            DB::connection('tenant')->beginTransaction();

            $priceLabel = PriceLabel::findOrFail($id);

            if ($priceLabel->is_default) {
                $priceLabel->update(['is_default' => false]);
                $message = 'Etiqueta de precio por defecto desmarcada';
            } else {
                if (!$priceLabel->is_active) {
                    DB::connection('tenant')->rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'No se puede marcar como por defecto una etiqueta inactiva',
                    ], 422);
                }

                PriceLabel::where('is_default', true)->update(['is_default' => false]);
                $priceLabel->update(['is_default' => true]);
                $message = 'Etiqueta de precio marcada como por defecto';
            }

            DB::connection('tenant')->commit();

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $priceLabel->fresh()->getCollectionData(),
            ]);

        } catch (Exception $e) {
            DB::connection('tenant')->rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la etiqueta por defecto: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reorganizar positions después de eliminar un label
     *
     * @return void
     */
    protected function reorganizePositions()
    {
        $labels = PriceLabel::ordered()->get();

        $position = 1;
        foreach ($labels as $label) {
            if ($label->position !== $position) {
                $label->update(['position' => $position]);
            }
            $position++;
        }
    }

    /**
     * Actualizar orden (posiciones) de los labels
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'labels' => 'required|array',
            'labels.*.id' => 'required|exists:tenant.price_labels,id',
            'labels.*.position' => 'required|integer|min:1',
        ]);

        try {
            DB::connection('tenant')->beginTransaction();

            foreach ($request->labels as $labelData) {
                PriceLabel::where('id', $labelData['id'])
                    ->update(['position' => $labelData['position']]);
            }

            DB::connection('tenant')->commit();

            return response()->json([
                'success' => true,
                'message' => 'Orden actualizado correctamente',
            ]);

        } catch (Exception $e) {
            DB::connection('tenant')->rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el orden: ' . $e->getMessage(),
            ], 500);
        }
    }
}
