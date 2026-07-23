<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Hyn\Tenancy\Contracts\CurrentHostname;
use App\Models\Tenant\StatusOrder;

class StatusOrdersController extends Controller
{
    /**
     * Clave de cache para los estados del pedido, scoped por tenant (fqdn).
     */
    protected function cacheKey(): string
    {
        $hostname = app(CurrentHostname::class);
        $fqdn = $hostname ? $hostname->fqdn : 'default';

        return "status_orders_{$fqdn}";
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
     * La colección se almacena en cache indefinidamente y se invalida en cada escritura.
     */
    public function records()
    {
        $statuses = Cache::rememberForever($this->cacheKey(), function () {
            return StatusOrder::orderBy('sort_order')->get();
        });

        return response()->json($statuses);
    }

    /**
     * Crea un nuevo estado de pedido.
     * El sort_order se asigna automáticamente como el siguiente valor disponible.
     * Si is_initial es true, se desactiva en todos los demás antes de guardar.
     */
    public function store(Request $request)
    {
        $request->validate([
            'description'              => 'required|string|max:255',
            'color'                    => 'nullable|string|max:7',
            'is_initial'               => 'boolean',
            'is_final'                 => 'boolean',
            'is_payment_status'        => 'boolean',
            'is_order_status'          => 'boolean',
            'is_shipping_status'       => 'boolean',
            'action_generate_document' => 'boolean',
            'action_discount_stock'    => 'boolean',
            'action_mark_payment'      => 'boolean',
            'action_send_email'        => 'boolean',
            'action_notify_dispatch'   => 'boolean',
            'action_generate_remission'=> 'boolean',
            'action_free_reserved_stock' => 'boolean',
            'action_block_returns'     => 'boolean',
            'action_void_order'        => 'boolean',
        ]);

        if ($request->boolean('is_initial')) {
            $typeColumn = $this->resolveTypeColumn($request);
            StatusOrder::where('is_initial', true)
                ->where($typeColumn, true)
                ->update(['is_initial' => false]);
        }

        // Estado final: único dentro del grupo de pedido
        if ($request->boolean('is_final')) {
            StatusOrder::where('is_final', true)
                ->where('is_order_status', true)
                ->update(['is_final' => false]);
        }

        // Asignar sort_order como el siguiente disponible
        $nextSortOrder = (StatusOrder::max('sort_order') ?? -1) + 1;

        $status = StatusOrder::create(array_merge(
            $request->only([
                'description', 'color', 'is_initial', 'is_final',
                'action_generate_document', 'action_discount_stock',
                'action_mark_payment', 'action_send_email',
                'action_notify_dispatch', 'action_generate_remission',
                'action_free_reserved_stock', 'action_block_returns',
                'action_void_order',
            ]),
            $this->resolveTypeFlags($request),
            ['sort_order' => $nextSortOrder]
        ));

        $this->flushCache();

        return response()->json([
            'success' => true,
            'message' => 'Estado creado correctamente',
            'data'    => $status,
        ]);
    }

    /**
     * Actualiza un estado existente.
     * Si is_initial es true, se desactiva en todos los demás antes de guardar.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'description'              => 'required|string|max:255',
            'color'                    => 'nullable|string|max:7',
            'is_initial'               => 'boolean',
            'is_final'                 => 'boolean',
            'is_payment_status'        => 'boolean',
            'is_order_status'          => 'boolean',
            'is_shipping_status'       => 'boolean',
            'action_generate_document' => 'boolean',
            'action_discount_stock'    => 'boolean',
            'action_mark_payment'      => 'boolean',
            'action_send_email'        => 'boolean',
            'action_notify_dispatch'   => 'boolean',
            'action_generate_remission'=> 'boolean',
            'action_free_reserved_stock' => 'boolean',
            'action_block_returns'     => 'boolean',
            'action_void_order'        => 'boolean',
        ]);

        $status = StatusOrder::findOrFail($id);

        if ($request->boolean('is_initial')) {
            $typeColumn = $this->resolveTypeColumn($request);
            StatusOrder::where('is_initial', true)
                ->where($typeColumn, true)
                ->where('id', '!=', $id)
                ->update(['is_initial' => false]);
        }

        // Estado final: único dentro del grupo de pedido
        if ($request->boolean('is_final')) {
            StatusOrder::where('is_final', true)
                ->where('is_order_status', true)
                ->where('id', '!=', $id)
                ->update(['is_final' => false]);
        }

        $status->update(array_merge(
            $request->only([
                'description', 'color', 'is_initial', 'is_final',
                'action_generate_document', 'action_discount_stock',
                'action_mark_payment', 'action_send_email',
                'action_notify_dispatch', 'action_generate_remission',
                'action_free_reserved_stock', 'action_block_returns',
                'action_void_order',
            ]),
            $this->resolveTypeFlags($request)
        ));

        $this->flushCache();

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente',
            'data'    => $status,
        ]);
    }

    /**
     * Actualiza el sort_order de múltiples estados en una sola petición.
     * Recibe un array de objetos: [{ id, sort_order }, ...]
     */
    public function reorder(Request $request)
    {
        // Validación básica de formato (no usar 'exists' por tenancy context)
        $request->validate([
            'order'              => 'required|array',
            'order.*.id'         => 'required|integer',
            'order.*.sort_order' => 'required|integer|min:0',
        ]);

        // Validación manual de existencia de los IDs en la tabla del tenant
        $ids = collect($request->order)->pluck('id')->unique()->values()->all();
        $found = StatusOrder::whereIn('id', $ids)->pluck('id')->all();

        $missing = array_values(array_diff($ids, $found));
        if (!empty($missing)) {
            return response()->json([
                'success' => false,
                'message' => 'Algunos estados no existen en este tenant',
                'missing_ids' => $missing,
            ], 422);
        }

        // Persistir nuevos sort_order
        foreach ($request->order as $item) {
            StatusOrder::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        $this->flushCache();

        return response()->json([
            'success' => true,
            'message' => 'Orden actualizado correctamente',
        ]);
    }

    /**
     * Resuelve los flags de tipo (pago / pedido / envío) garantizando exclusividad mutua.
     * Prioridad si llegan varios: pago > envío > pedido. Si ninguno llega, por defecto "pedido".
     */
    protected function resolveTypeFlags(Request $request): array
    {
        $isPayment  = $request->boolean('is_payment_status');
        $isShipping = $request->boolean('is_shipping_status');
        $isOrder    = $request->boolean('is_order_status');

        if ($isPayment) {
            return ['is_payment_status' => true, 'is_order_status' => false, 'is_shipping_status' => false];
        }
        if ($isShipping) {
            return ['is_payment_status' => false, 'is_order_status' => false, 'is_shipping_status' => true];
        }
        if ($isOrder) {
            return ['is_payment_status' => false, 'is_order_status' => true, 'is_shipping_status' => false];
        }

        return ['is_payment_status' => false, 'is_order_status' => true, 'is_shipping_status' => false];
    }

    /**
     * Devuelve la columna de tipo activa para la petición (misma prioridad que resolveTypeFlags).
     * Se usa para acotar el "estado inicial" al mismo grupo.
     */
    protected function resolveTypeColumn(Request $request): string
    {
        if ($request->boolean('is_payment_status')) {
            return 'is_payment_status';
        }
        if ($request->boolean('is_shipping_status')) {
            return 'is_shipping_status';
        }
        return 'is_order_status';
    }

    /**
     * Elimina un estado. Solo se permite si no tiene pedidos asociados.
     */
    public function destroy($id)
    {
        $status = StatusOrder::findOrFail($id);

        if ($status->order()->count() > 0 || $status->payment_order()->count() > 0 || $status->shipping_order()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar, tiene pedidos asociados',
            ]);
        }

        $status->delete();

        $this->flushCache();

        return response()->json([
            'success' => true,
            'message' => 'Estado eliminado correctamente',
        ]);
    }
}

