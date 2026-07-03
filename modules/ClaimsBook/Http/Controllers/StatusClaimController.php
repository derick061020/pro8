<?php

// Modules/ClaimsBook/Http/Controllers/StatusClaimController.php

namespace Modules\ClaimsBook\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use Hyn\Tenancy\Contracts\CurrentHostname;
use Modules\ClaimsBook\Models\Tenant\StatusClaim;

/**
 * Gestión de estados del libro de reclamaciones.
 * Análogo a StatusOrdersController con soporte adicional para is_final.
 * El listado se almacena en cache por tenant e invalida en cada mutación.
 */
class StatusClaimController extends Controller
{
    /**
     * Clave de cache para los estados del módulo, scoped por tenant.
     */
    protected function cacheKey(): string
    {
        $hostname = app(CurrentHostname::class);
        $fqdn     = $hostname ? $hostname->fqdn : 'default';

        return "status_claims_{$fqdn}";
    }

    /**
     * Invalida el cache de estados para el tenant actual.
     */
    protected function flushCache(): void
    {
        Cache::forget($this->cacheKey());
    }

    /**
     * Retorna todos los estados ordenados por sort_order.
     * Resultado almacenado en cache indefinidamente e invalidado en cada escritura.
     */
    public function records()
    {
        $statuses = Cache::rememberForever($this->cacheKey(), function () {
            return StatusClaim::orderBy('sort_order')->get()->map->getCollectionData()->values();
        });

        return response()->json($statuses);
    }

    /**
     * Crea un nuevo estado de reclamo.
     * Si is_initial o is_final es true, desactiva el flag en los demás estados antes de guardar.
     * El sort_order se asigna como el siguiente valor disponible.
     */
    public function store(Request $request)
    {
        $request->validate([
            'description'       => 'required|string|max:60',
            'color'             => 'nullable|string|max:7',
            'is_initial'        => 'boolean',
            'is_final'          => 'boolean',
            'action_send_email' => 'boolean',
            'assigned_user_id'  => 'nullable|integer',
        ]);

        // Garantizar unicidad de is_initial
        if ($request->boolean('is_initial')) {
            StatusClaim::where('is_initial', true)->update(['is_initial' => false]);
        }

        // Garantizar unicidad de is_final
        if ($request->boolean('is_final')) {
            StatusClaim::where('is_final', true)->update(['is_final' => false]);
        }

        // is_initial e is_final son mutuamente excluyentes
        if ($request->boolean('is_initial') && $request->boolean('is_final')) {
            return response()->json([
                'success' => false,
                'message' => 'Un estado no puede ser inicial y final al mismo tiempo',
            ], 422);
        }

        $nextSortOrder = (StatusClaim::max('sort_order') ?? -1) + 1;

        $status = StatusClaim::create(array_merge(
            $request->only(['description', 'color', 'is_initial', 'is_final', 'action_send_email', 'assigned_user_id']),
            ['sort_order' => $nextSortOrder]
        ));

        $this->flushCache();

        return response()->json([
            'success' => true,
            'message' => 'Estado creado correctamente',
            'data'    => $status->getCollectionData(),
        ]);
    }

    /**
     * Actualiza un estado existente.
     * Mantiene la unicidad de is_initial e is_final.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'description'       => 'required|string|max:60',
            'color'             => 'nullable|string|max:7',
            'is_initial'        => 'boolean',
            'is_final'          => 'boolean',
            'action_send_email' => 'boolean',
            'assigned_user_id'  => 'nullable|integer',
        ]);

        $status = StatusClaim::findOrFail($id);

        // is_initial e is_final son mutuamente excluyentes
        if ($request->boolean('is_initial') && $request->boolean('is_final')) {
            return response()->json([
                'success' => false,
                'message' => 'Un estado no puede ser inicial y final al mismo tiempo',
            ], 422);
        }

        // Garantizar unicidad de is_initial
        if ($request->boolean('is_initial')) {
            StatusClaim::where('is_initial', true)
                ->where('id', '!=', $id)
                ->update(['is_initial' => false]);
        }

        // Garantizar unicidad de is_final
        if ($request->boolean('is_final')) {
            StatusClaim::where('is_final', true)
                ->where('id', '!=', $id)
                ->update(['is_final' => false]);
        }

        $status->update(
            $request->only(['description', 'color', 'is_initial', 'is_final', 'action_send_email', 'assigned_user_id'])
        );

        $this->flushCache();

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente',
            'data'    => $status->fresh()->getCollectionData(),
        ]);
    }

    /**
     * Elimina un estado. No se permite si tiene reclamos asociados.
     */
    public function destroy($id)
    {
        $status = StatusClaim::findOrFail($id);

        if ($status->claims()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar, tiene reclamos asociados',
            ]);
        }

        $status->delete();

        $this->flushCache();

        return response()->json([
            'success' => true,
            'message' => 'Estado eliminado correctamente',
        ]);
    }

    /**
     * Actualiza el sort_order de múltiples estados en una sola petición.
     * Recibe un array de objetos: [{ id, sort_order }, ...]
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'order'              => 'required|array',
            'order.*.id'         => 'required|integer',
            'order.*.sort_order' => 'required|integer|min:0',
        ]);

        // Validación manual de IDs en el contexto del tenant
        $ids     = collect($request->order)->pluck('id')->unique()->values()->all();
        $found   = StatusClaim::whereIn('id', $ids)->pluck('id')->all();
        $missing = array_values(array_diff($ids, $found));

        if (! empty($missing)) {
            return response()->json([
                'success'     => false,
                'message'     => 'Algunos estados no existen en este tenant',
                'missing_ids' => $missing,
            ], 422);
        }

        foreach ($request->order as $item) {
            StatusClaim::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        $this->flushCache();

        return response()->json([
            'success' => true,
            'message' => 'Orden actualizado correctamente',
        ]);
    }
}
